class AnalysisJob < ActiveJob::Base
  def perform
    purchase_table = {}
    # 결제 내역 받아오기
    user_ids = Purchase.select(:user_id).uniq
    user_ids.each do |u|
      purchase_table[u] = []
      purchases = Purchase.where(user_id: u.user_id)
      purchases.each do |p|
        purchase_table[u].push(p.product_id)
      end
    end
    # 누가 누가 닮았을까
    user_ids.each do |me|
      best_match = nil
      best_score = 0
      user_ids.each do |other|
        next if me == other
        union = (purchase_table[me] | purchase_table[other]).size
        intersection = (union - (purchase_table[other] + purchase_table[me]).size).abs
        ratio = intersection / union.to_f
        if ratio > best_score
          best_score = ratio
          best_match = other
        end
      end
      # 가장 닮은 친구의 상품 중 겹치지 않는걸 추천해주자
      if best_match
        Recommand.destroy_all(user_id: me.user_id)
        recommands = purchase_table[best_match] - purchase_table[me]
        for recommand in recommands
          r = Recommand.new(user_id: me.user_id, product_id: recommand)
          r.save
        end
      end
    end
    AnalysisJob.delay_for(1.day).perform_later
  end
end
